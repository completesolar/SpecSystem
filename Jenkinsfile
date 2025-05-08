pipeline {
    agent any

    environment {
        BRANCH = "${env.CHANGE_BRANCH ?: env.BRANCH_NAME}"
    }

    triggers {
        githubPush()
        pollSCM('H/2 * * * *')
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
                sh 'chmod +x deploy/deploy.sh' // Ensure script is executable before transfer
            }
        }

        stage('Deploy to Remote Host') {
            steps {
                script {
                    def remote = [:]
                    remote.name = 'specsystem-prod-ami-test'
                    remote.user = 'jenkins'               // Needed if not in Jenkins global config
                    remote.host = '10.121.121.83' 
                    remote.allowAnyHosts = true           // Skip host key checking

                    // If you're using Jenkins credentials plugin:
                    remote.identityFile = '/var/lib/jenkins/.ssh/id_rsa' 
                    // OR use 'credentialsId' if configured in Jenkins

                    sshPut remote: remote, from: 'deploy/deploy.sh', into: '/tmp/deploy.sh'

                    sshCommand remote: remote, command: """
                        set -e
                        echo "Running deploy.sh on branch: ${BRANCH}"
                        chmod +x /tmp/deploy.sh
                        BRANCH=${BRANCH} bash /tmp/deploy.sh
                    """
                }
            }
        }
    }

    post {
        success {
            echo 'Deployment completed successfully.'
        }
        failure {
            echo 'Deployment failed. Check the logs for details.'
        }
    }
}
