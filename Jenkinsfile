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
                    remote.user = 'jenkins'               // SSH user
                    remote.host = '10.121.121.83' 
                    remote.allowAnyHosts = true           // Skip host key checking for SSH

                    // If you're using Jenkins credentials plugin:
                    remote.identityFile = '/var/lib/jenkins/.ssh/id_rsa'  // Path to the private key for SSH authentication
                    // OR use 'credentialsId' if configured in Jenkins

                    // Transfer the deploy.sh script to the remote host
                    sshPut remote: remote, from: 'deploy/deploy.sh', into: '/tmp/deploy.sh'

                    // Execute the deploy.sh script on the remote host
                    sshCommand remote: remote, command: """
                        set -e
                        echo "Running deploy.sh on branch: ${BRANCH}"
                        chmod +x /tmp/deploy.sh  # Ensure the script is executable
                        BRANCH=${BRANCH} bash /tmp/deploy.sh  # Execute the script with the branch variable
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
