pipeline {
    agent any

    environment {
        BRANCH = "${env.GIT_BRANCH ?: env.BRANCH_NAME ?: 'feature/devops-changes'}"
    }

    triggers {
        githubPush()
        
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
                sh 'chmod +x deploy/deploy.sh'
                sh 'echo "Resolved branch: ${BRANCH}"'
            }
        }
        stage('Verify Workspace') {
            steps {
                sh '''
                    echo "Current working directory:"
                    pwd
                    echo "Directory listing:"
                    ls -al                                     
                    echo "Jenkinsfile:"
                    cat Jenkinsfile
                '''
            }
        }

        stage('Deploy to Remote Host') {
            steps {
                script {
                    def remote = [:]
                    remote.name = 'specsystem-prod-ami-test'
                    remote.user = 'jenkins'
                    remote.host = '10.121.121.83'
                    remote.allowAnyHosts = true
                    remote.identityFile = '/var/lib/jenkins/.ssh/id_rsa'

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
