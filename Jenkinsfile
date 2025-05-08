pipeline {
    agent any

    environment {
        BRANCH = "${env.CHANGE_BRANCH ?: env.BRANCH_NAME}"
    }

    triggers {
        githubPullRequest()
        pollSCM('H/5 * * * *')
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
                    remote.allowAnyHosts = true

                    sshPut remote: remote, from: 'deploy/deploy.sh', into: '/tmp/deploy.sh'

                    sshCommand remote: remote, command: """
                        chmod +x /tmp/deploy.sh &&
                        BRANCH=${BRANCH} bash /tmp/deploy.sh
                    """
                }
            }
        }
    }

    post {
        success {
            echo 'Deployment successful'
        }
        failure {
            echo 'Deployment failed'
        }
    }
}
