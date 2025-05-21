pipeline {
    agent any

    environment {
        BRANCH = "${env.BRANCH_NAME ?: 'feature/devops-changes'}"
        CHANGE_TARGET = "${env.CHANGE_TARGET ?: ''}"
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
                sh 'chmod +x deploy/deploy.sh'
                sh 'echo "Resolved branch: ${BRANCH}"'
                sh 'echo "Change target: ${CHANGE_TARGET}"'
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
            when {
                allOf {
                    branch pattern: "feature/.*", comparator: "REGEXP"
                    environment name: 'CHANGE_TARGET', value: 'develop'
                }
            }
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
                        export BRANCH="${BRANCH}"
                        echo \$BRANCH
                        bash /tmp/deploy.sh
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
