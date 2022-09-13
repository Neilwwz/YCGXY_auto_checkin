import os
from selenium import webdriver
from time import sleep
import ddddocr
from six.moves import urllib

ocr = ddddocr.DdddOcr()
options = webdriver.ChromeOptions()
options.add_argument("no-sandbox")
options.add_argument("--disable-gpu")
options.add_argument("--disable-dev-shm-usage")
options.add_argument("user-agent=Mozilla/5.0 (Linux; Android 11; Pixel 4a Build/RP1A.200720.005; wv) "
                     "AppleWebKit/537.36 (KHTML, "
                     "like Gecko) Version/4.0 Chrome/84.0.4147.111 Mobile Safari/537.36")
driver = webdriver.Chrome(options=options)

driver.set_window_size(768, 1024)

def login(username, password):
    driver.get("https://health.xiamin.tech/user/profile/")
    driver.find_element("xpath", "/html/body/div[2]/div/div[4]/div/button").click()
    driver.find_element("id", "id_username").send_keys(username)
    driver.find_element("id", "id_password").send_keys(password)
    img = driver.find_element("xpath", "/html/body/div[1]/div/div/div/div[2]/div[2]/form/div[3]/img")
    url = img.get_attribute('src')
    filename = url.split('/')[-2]
    urllib.request.urlretrieve(url, f"tmp/{filename}")
    captcha = open(f"tmp/{filename}", "rb").read()
    code = ocr.classification(captcha)
    print(code)
    driver.find_element("id", "id_captcha_1").send_keys(code)
    driver.find_element("xpath", "/html/body/div[1]/div/div/div/div[2]/div[2]/form/button").click()
    try:
        driver.find_element("xpath", "/html/body/div[1]/div[3]/div[2]/div/div/div/div/div[1]/div")
    except BaseException:
        print("登录失败")
        return False
    else:
        print("登录成功")
        return True


while not (result := login("321102200212261019", "123456wqz")):
    pass

# driver.get("https://health.xiamin.tech/user/profile/")
# driver.add_cookie({"name": "sessionid", "value": "tmclng077iq2rjs5ll2uxn3svyzdriwt", "domain": "health.xiamin.tech"})
driver.refresh()
if driver.find_element("id", "regist_button").text == "今日已上报":
    print("今日已上报")
else:
    driver.find_element("id", "regist_button").click()
    sleep(3)
    driver.find_element("xpath", '//*[@id="app"]/div[5]/div[2]/button[2]').click()
    print("上报成功")

driver.close()

for file in os.listdir("tmp"):
    os.remove(f"tmp/{file}")