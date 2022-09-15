FROM python:slim-bullseye

WORKDIR /app

ADD auto.py /app
ADD requirements.txt /app

RUN pip install -r requirements.txt

ENV webdriver=""
ENV username=""
ENV password=""
ENV wxpusher_uid=""

CMD python -u auto.py -webdriver=$webdriver -username=$username -password=$password -wxpusher_uid=$wxpusher_uid
