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
                sh '''
                    echo "Resolved branch: ${BRANCH}"
                    echo "Change target: ${CHANGE_TARGET}"
                    echo "Job name: ${JOB_NAME}"
                    echo "Branch name: ${BRANCH_NAME}"
                    echo "Change branch: ${CHANGE_BRANCH}"
                    echo "Change target: ${CHANGE_TARGET}"
                '''
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
                expression {
                    def isFeaturePRToDev = env.BRANCH ==~ /^feature\/.*/ && env.CHANGE_TARGET == "develop"
                    def isDevelopBranch = env.BRANCH == "develop"
                    def isMainBranch = env.BRANCH == "main"

                    def isDevJob = env.JOB_NAME.contains("spec-system-dev")
                    def isProdJob = env.JOB_NAME.contains("spec-system-prod")

                    // Run only in Dev job for feature→develop PRs or develop push
                    if (isDevJob && (isFeaturePRToDev || isDevelopBranch)) {
                        return true
                    }

                    // Run only in Prod job for main branch
                    if (isProdJob && isMainBranch) {
                        return true
                    }

                    // Otherwise skip
                    return false
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
                        remote.host = '10.121.121.83' // Dev Host
                    } else if (env.BRANCH == "develop") {
                        remote.name = 'specsystem-prod-ami-test'
                        remote.host = '10.121.121.83' // Dev Host
                    } else if (env.BRANCH == "main") {
                        remote.name = 'specsystem-cs-prod-1'
                        remote.host = '10.125.121.54' // Prod Host
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
