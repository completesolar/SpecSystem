pipeline {
    agent any

    environment {
        REMOTE_USER = 'jenkins'
        REMOTE_HOST = '10.121.121.83'
        REMOTE_DEPLOY_DIR = '/home/jenkins/deploy'
        SSH_KEY_ID = 'cs-prod-1' // Jenkins credential ID for SSH key
    }

    parameters {
        string(name: 'BRANCH_NAME', defaultValue: 'feature/devops-changes', description: 'Git branch to deploy')
    }

    options {
        disableConcurrentBuilds()
        timestamps()
        timeout(time: 15, unit: 'MINUTES')
    }

    stages {
        stage('Checkout') {
            steps {
                git branch: "${params.BRANCH_NAME}",
                    url: 'https://github.com/completesolar/SpecSystem.git'
            }
        }

        stage('Verify Workspace') {
            steps {
                sh '''
                    echo "Current working directory:"
                    pwd
                    echo "Directory listing:"
                    ls -al
                    echo "Git branch:"
                    git branch
                '''
            }
        }

        // stage('Prepare and Deploy') {
            steps {
                sshagent (credentials: [env.SSH_KEY_ID]) {
                    sh """
                        ssh -o StrictHostKeyChecking=no ${REMOTE_USER}@${REMOTE_HOST} "mkdir -p '${REMOTE_DEPLOY_DIR}'"
                        scp -o StrictHostKeyChecking=no deploy/deploy.sh ${REMOTE_USER}@${REMOTE_HOST}:'${REMOTE_DEPLOY_DIR}/deploy.sh'
                        ssh -o StrictHostKeyChecking=no ${REMOTE_USER}@${REMOTE_HOST} "chmod +x '${REMOTE_DEPLOY_DIR}/deploy.sh' && BRANCH='${params.BRANCH_NAME}' '${REMOTE_DEPLOY_DIR}/deploy.sh'"
                    """
                }
            }
        }
    }

    post {
        always {
            cleanWs()
        }
        failure {
            echo "Deployment failed. Please check the logs."
        }
        success {
            echo "Deployment completed successfully!"
        }
    }
}
