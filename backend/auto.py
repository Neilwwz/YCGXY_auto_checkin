import argparse
import datetime
import logging
import os
import random
import time
from time import sleep

import ddddocr
import pytz
import requests
import schedule
from selenium import webdriver
from six.moves import urllib
from tzlocal import get_localzone

parser = argparse.ArgumentParser(description="")
parser.add_argument("-webdriver", default="")
parser.add_argument("-username", default="")
parser.add_argument("-password", default="")
parser.add_argument("-wxpusher_uid", default="")
args = parser.parse_args()


class Config:
    def __init__(self):
        self.wxpusher_enable = False
        self.webdriver = args.webdriver
        self.username = args.username
        self.password = args.password
        if args.wxpusher_uid != "":
            self.wxpusher_uid = args.wxpusher_uid
            self.wxpusher_enable = True
        self.tzlondon = pytz.timezone("Asia/Shanghai")  # Time zone
        self.tzlocal = get_localzone()


config = Config()

ocr = ddddocr.DdddOcr()

ua_list = [
    "Mozilla/5.0 (Linux; U; Android 10; zh-CN; MI 9 Build/QKQ1.190825.002) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/78.0.3904.108 UCBrowser/13.1.9.1099 Mobile Safari/537.36",
    "Mozilla/5.0 (Linux; Android 10; GM1910 Build/QKQ1.190716.003; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/76.0.3809.89 Mobile Safari/537.36 T7/12.5 SP-engine/2.26.0 baiduboxapp/12.5.0.11 (Baidu; P1 10) NABar/1.0",
    "Mozilla/5.0 (Linux; U; Android 11; zh-CN; Redmi K30 Pro Build/RKQ1.200826.002) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/78.0.3904.108 Quark/5.1.2.182 Mobile Safari/537.36",
    "Mozilla/5.0 (Linux; Android 13) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.5195.124 Mobile Safari/537.36",
    "Mozilla/5.0 (Android 13; Mobile; rv:68.0) Gecko/68.0 Firefox/104.0",
    "Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.6 Mobile/15E148 Safari/604.1",
    "Mozilla/5.0 (Linux; Android 13; Pixel 6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.5195.124 Mobile Safari/537.36",]


def setup_driver():
    global driver
    options = webdriver.ChromeOptions()
    options.add_argument("no-sandbox")
    options.add_argument("--disable-gpu")
    options.add_argument("--disable-dev-shm-usage")
    options.add_argument(f"user-agent={random.choice(ua_list)}")
    driver = webdriver.Remote(command_executor=config.webdriver, options=options)
    driver.set_page_load_timeout(30)


class WXpusher:
    def __init__(self):
        self.API_TOKEN = "AT_KdK1PVMnXB0tfV2fAKMgYvSpra00bXUN"
        self.baseurl = f"http://wxpusher.zjiecode.com/api/send/message/?appToken={self.API_TOKEN}\
        &uid={config.wxpusher_uid}&content="

    def sendmessage(self, content):
        requests.get(f"{self.baseurl}{content}")


if config.wxpusher_enable:
    wxpusher = WXpusher()


def notification(content, error=False):
    info(content)
    if error:
        content = f"[ERROR] {content}"
    if config.wxpusher_enable:
        wxpusher.sendmessage(content)


def info(text):
    print(text)
    logging.info(text)


def error(text, time_hold=300):
    notification(text)
    logging.critical(text)
    driver.quit()
    time.sleep(time_hold)
    main()
    exit()


class User:
    def __init__(self):
        self.username = config.username
        self.password = config.password

    def login(self):
        driver.get("https://health.xiamin.tech/login/?next=/user/profile/")
        try:
            driver.find_element("xpath", "/html/body/div[2]/div/div[4]/div/button").click()
        except BaseException:
            pass
        try:
            driver.find_element("xpath", "/html/body/div[1]/div/div/div/div[2]/div[2]/form/button")
        except BaseException:
            info("已经登陆")
            return True
        driver.find_element("id", "id_username").send_keys(self.username)
        driver.find_element("id", "id_password").send_keys(self.password)
        img = driver.find_element("xpath", "/html/body/div[1]/div/div/div/div[2]/div[2]/form/div[3]/img")
        url = img.get_attribute('src')
        filename = url.split('/')[-2]
        urllib.request.urlretrieve(url, f"{filename}")
        captcha = open(f"{filename}", "rb").read()
        code = ocr.classification(captcha)
        driver.find_element("id", "id_captcha_1").send_keys(code)
        os.remove(f"{filename}")
        driver.find_element("xpath", "/html/body/div[1]/div/div/div/div[2]/div[2]/form/button").click()
        try:
            driver.find_element("xpath", "/html/body/div[1]/div[3]/div[2]/div/div/div/div/div[1]/div")
        except BaseException:
            info("登录失败")
            return False
        else:
            info("登录成功")
            return True

    def checkin(self):
        driver.refresh()
        if driver.find_element("id", "regist_button").get_attribute("disabled") == "true":
            info("检测到今日已上报")
        else:
            driver.find_element("id", "regist_button").click()
            sleep(3)
            driver.find_element("xpath", '//*[@id="app"]/div[5]/div[2]/button[2]').click()
            notification("今日自动上报成功")


def randomtime():  # 随机时间
    return modifytime(random.randint(1, 7), random.randint(1, 59), random.randint(1, 59))


def modifytime(hh, mm, ss):  # 换算时区
    date = datetime.date.today().strftime("%Y/%m/%d").split("/")
    time = datetime.datetime(int(date[0]), int(date[1]), int(date[2]), hh, mm, ss)
    time = config.tzlondon.localize(time)
    time = time.astimezone(config.tzlocal)
    return [time.strftime('%H:%M:%S'), f"{hh}:{mm}:{ss}"]  # 修正时区|上海时区


def job():
    setup_driver()
    while not (user.login()):
        pass
    user.checkin()
    schedule.clear()
    nexttime = randomtime()
    schedule.every().day.at(nexttime[0]).do(job)
    info(f"已设置下次执行时间（本地时区）：{nexttime[0]}")
    info(f"已设置下次执行时间：{nexttime[1]}")
    driver.quit()


def main():
    notification("自动签到开始运行")
    global user
    user = User()
    job()
    while True:
        schedule.run_pending()
        time.sleep(60)


if __name__ == '__main__':
    main()
