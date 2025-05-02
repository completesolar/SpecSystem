#!/bin/bash

RED='\033[0;31m'
GREEN='\033[0;32m'
NC='\033[0m'


echo -e "${GREEN}Parameters${NC}"
echo -e "${GREEN}=======================${NC}"
echo -e "${RED}User:${NC}"
whoami
echo -e "${GREEN}=======================${NC}"


#Function check result previously command
function result() {
    $1
    if [ $? != 0 ]
    then
        RED='\033[0;31m'
        NC='\033[0m'
        echo -e "${RED}ERROR!!! Command: ${1}${NC}"
        exit 1
    else
        GREEN='\033[0;32m'
        NC='\033[0m'
        echo -e "${GREEN}SUCCESS command: ${1}${NC}"
    fi
}

####Deploy######
cd /opt/specrepo
sudo docker compose down
sudo rm -rf /tmp/SpecSystem
cd /tmp
result "sudo git clone -b $BRANCH https://github.com/completesolar/SpecSystem.git"
echo -e "${GREEN}=====THE CODE IS COPIED=====${NC}"
sudo aws s3 cp s3://solar-info-cs-biz-1/deploy/biz/specsystem/docker-compose.yml /tmp/SpecSystem/docker-compose.yml
sudo aws s3 cp s3://solar-info-cs-biz-1/deploy/biz/specsystem/Dockerfile /tmp/SpecSystem/spec/Dockerfile
sudo aws s3 cp s3://solar-info-cs-biz-1/deploy/biz/specsystem/nginx.conf /tmp/SpecSystem/spec/nginx.conf
sudo aws s3 cp s3://solar-info-cs-biz-1/deploy/biz/specsystem/settings.py /tmp/SpecSystem/spec/proj/settings.py
sudo aws s3 cp s3://solar-info-cs-biz-1/deploy/biz/specsystem/.env /tmp/SpecSystem/spec/ui/.env
#sudo docker ps -q | sudo xargs docker stop
sudo rm -rf /opt/specrepo_old 
sudo mv -f /opt/specrepo /opt/specrepo_old 
sudo mv -f /tmp/SpecSystem /opt/specrepo
cd /opt/specrepo
sudo docker compose build --no-cache --progress=plain
sudo docker compose up -d
sudo docker compose exec memosystem bash -c "cd ui/ && npm install && npm run build:dev && cd .. && cp -r ./frontend/static/* ./static"


sudo docker system prune -f
#echo -e "${GREEN}=====OLD DOCKER CONTAINERS DELETED=====${NC}"
echo -e "${GREEN}=======================${NC}"
sleep 10s
docker_status="$(sudo docker ps -a)"
echo "${docker_status}"
echo -e "${GREEN}=======================${NC}"
