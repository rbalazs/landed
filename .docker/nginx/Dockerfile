FROM ubuntu:16.04

WORKDIR /app

RUN apt-get update && apt-get install -y apt-utils nano vim curl net-tools dialog lsof bash nginx && apt-get clean && rm -rf /var/lib/apt/lists/*

EXPOSE 80

CMD ["nginx"]
