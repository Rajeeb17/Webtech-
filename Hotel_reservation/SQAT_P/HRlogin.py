from selenium import webdriver
from selenium.webdriver.common.by import By
import time

# 1. Start the Chrome Browser
driver = webdriver.Chrome()

try:
    # 2. Navigate to Localhost Login Page
    driver.get("http://localhost/Hotel_reservation/login.php")
    driver.maximize_window()
    print("Step 1: Hotel Reservation Login Page opened.")

    # 3. load
    time.sleep(2)

   # STEP 1: Login
    driver.get("http://localhost/Hotel_reservation/login.php")
    driver.maximize_window()
    driver.find_element(By.NAME, "username").send_keys("aja@gmail.com")
    driver.find_element(By.NAME, "loginPassword").send_keys("0987654321")
    driver.find_element(By.CLASS_NAME, "login-btn").click()
    print("Step 2: Login successful.")
    time.sleep(3)

finally:
    # 8. End
    driver.quit()
    print("Step 3: Test completed. Browser processes terminated.")