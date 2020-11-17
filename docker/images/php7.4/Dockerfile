FROM webdevops/php-nginx-dev:7.4

# ARG GITHUB_TOKEN
# ENV COMPOSER_AUTH='{"github-oauth":{"github.com":"'$GITHUB_TOKEN'"}}'

RUN apt-get update && \
    apt-get install -y iputils-ping

## add github token
# RUN mkdir /root/.ssh/ && \
#     echo $COMPOSER_AUTH > /root/.composer/auth.json && \
#     chmod -R 600 /root/.ssh/ && \
#     chmod -R 600 /root/.composer/ && \
#     ssh-keyscan -t rsa github.com >> ~/.ssh/known_hosts

## autocomplete git
RUN /bin/bash -c "source /usr/share/bash-completion/completions/git"
RUN echo 'alias ll="ls -lha"' >> ~/.bashrc