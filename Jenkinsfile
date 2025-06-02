pipeline {
    agent any

    environment {
        BRANCH = "${env.CHANGE_BRANCH ?: env.BRANCH_NAME}"
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
                anyOf {
                    allOf {
                        expression {
                            return env.BRANCH ==~ /^feature\/.*/ && env.CHANGE_TARGET == "develop"
                        }
                    }
                    expression {
                        return env.BRANCH == "develop" || env.BRANCH == "main"
                    }
                }
            }
            steps {
                script {
                    def remote = [:]
                    remote.user = 'jenkins'
                    remote.allowAnyHosts = true
                    remote.identityFile = '/var/lib/jenkins/.ssh/id_rsa'

                    if (env.BRANCH ==~ /^feature\/.*/ && env.CHANGE_TARGET == "develop") {
                        remote.name = 'specsystem-prod-ami-test'
                        remote.host = '10.121.121.83'
                    } else if (env.BRANCH == "develop") {
                        remote.name = 'specsystem-prod-ami-test'
                        remote.host = '10.121.121.83'
                    } else if (env.BRANCH == "main") {
                        remote.name = 'specsystem-cs-prod-1'
                        remote.host = '10.125.121.54'
                    }

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
