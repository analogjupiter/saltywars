# syntax=docker/dockerfile:1

# == builder ==
FROM docker.io/debian:bookworm-slim AS builder
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update && apt-get -y install \
    build-essential \
    gdc \
    dub

# BuildKit or Podman buildah ^4.0
#RUN --mount=type=cache,target=/var/www/.dub,uid=33,gid=33 dub build

# Legacy Docker builder or buildah 3.x
RUN mkdir -p /var/www/.dub
RUN chown www-data:www-data /var/www/.dub

COPY --chown=www-data:www-data . /src

USER www-data:www-data
WORKDIR /src
RUN dub build

# == runtime ==
FROM docker.io/debian:bookworm-slim AS runtime
RUN apt-get update && apt-get -y install libgphobos3
USER www-data:www-data
ENTRYPOINT /saltywars/server
COPY --from=builder /src/bin/saltywars-srv /saltywars/server
