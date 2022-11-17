# We need a golang build environment first
FROM golang:1.17.0-alpine3.13

WORKDIR /go/src/app
ADD visualpay.go /go/src/app

RUN go build visualpay.go

# We use a Docker multi-stage build here in order that we only take the compiled go executable
FROM alpine:3.14

LABEL org.opencontainers.image.source="https://github.com/wuemeli/visualpay"

COPY --from=0 "/go/src/app/visualpay" hello-world

ENTRYPOINT ./visualpay
