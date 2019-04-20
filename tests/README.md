Test environment
================

To start test application you need execute commands bellow:

```sh
make build
docker-compose up
```

And the application will be available by URL http://localhost:8282/.

If you need to add few workers, start containers with:

```sh
docker-compose up --scale queue=5
```

To run yii command you need execute a command kind of:

```sh
docker-compose exec web php tests/yii migrate
```

To extract translation messages:  

```sh
docker-compose exec web php tests/yii message/extract
```
