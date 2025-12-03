FROM node:20-alpine

WORKDIR /app

COPY package*.json ./

RUN npm ci

COPY . .

RUN rm -rf src/**/*.d.ts && \
    npm run build:docker

EXPOSE 3000

CMD ["npm", "run", "dev"]
