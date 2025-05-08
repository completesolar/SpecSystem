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
            }
        }

        stage('Deploy to Remote Host') {
            steps {
                script {
                    def remote = [:]
                    remote.name = 'specsystem-prod-ami-test'
                    remote.user = 'jenkins'  // Required even if user is configured in Jenkins
                    remote.allowAnyHosts = true

                    sshPut remote: remote, from: 'deploy/deploy.sh', into: '/tmp/deploy.sh'

                    sshCommand remote: remote, command: """
                        echo "Running deploy.sh on branch: ${BRANCH}"
                        chmod +x /tmp/deploy.sh &&
                        BRANCH=${BRANCH} bash /tmp/deploy.sh
                    """
                }
            }
        }
    }

    post {
        success {
            echo 'Deployment completed successful'
        }
        failure {
            echo 'Deployment failed'
        }
    }
}
