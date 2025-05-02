pipeline {
    agent any

    environment {
        DEPLOY_BRANCH = 'feature/devops-changes'
        SSH_USER = 'ubuntu'                      
        SSH_HOST = '10.121.121.83'
        REMOTE_PATH = '/home/ubuntu/deploy'
        SSH_KEY_ID = 'cs-prod-1.pem'     // Jenkins credentials ID for SSH key
    }

    triggers {
        pollSCM('* * * * *')  
    }

    options {
        disableConcurrentBuilds()
        timestamps()
    }

    stages {
        stage('Checkout') {
            when {
                branch "feature/devops-changes"
            }
            steps {
                git branch: 'feature/devops-changes',
                    url: 'https://github.com/completesolar/SpecSystem.git'
            }
        }

        stage('Deploy to Remote Server') {
            steps {
                sshagent (credentials: [env.SSH_KEY_ID]) {
                    sh """
                        ssh -o StrictHostKeyChecking=no ${SSH_USER}@${SSH_HOST} 'mkdir -p ${REMOTE_PATH}'
                        scp -o StrictHostKeyChecking=no deploy/deploy.sh ${SSH_USER}@${SSH_HOST}:${REMOTE_PATH}/deploy.sh
                        ssh -o StrictHostKeyChecking=no ${SSH_USER}@${SSH_HOST} 'chmod +x ${REMOTE_PATH}/deploy.sh && BRANCH=feature/devops-changes ${REMOTE_PATH}/deploy.sh'
                    """
                }
            }
        }
    }

    post {
        failure {
            mail to: 'you@example.com',
                 subject: "Failed Deployment on ${env.BUILD_URL}",
                 body: "Pipeline failed for branch: ${env.BRANCH_NAME}"
        }
    }
}
