FROM debian:jessie
MAINTAINER Alexander Fedyashov <a@fedyashov.com>

ADD stretch.list /etc/apt/sources.list.d/
ADD preferences /etc/apt/preferences
RUN apt-get update && apt-get install -y --force-yes \
    build-essential git \
    devscripts debhelper fakeroot

RUN echo '%adm ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers
ENV LC_ALL="C.UTF-8" LANG="C.UTF-8"

# Install dependencies to speed up builds
RUN apt-get update && apt-get install -y -t stretch \
    libgumbo-dev
