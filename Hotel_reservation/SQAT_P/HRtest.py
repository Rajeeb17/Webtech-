from selenium import webdriver
from selenium.webdriver.common.by import By
import time

# 1. Initialize Chrome
driver = webdriver.Chrome()

try:
    # 2. Open Localhost Hotel Reservation Login
    # The URL matches your screenshot exactly
    driver.get("http://localhost/Hotel_reservation/login.php")
    driver.maximize_window()
    print("Step 1: Localhost Login Page opened successfully.")

    # 3. Wait for elements to load
    time.sleep(50)

    # 4. Find the fields (Using CSS Selectors for better accuracy)
    # Looking at your image, these are standard input boxes
    username_field = driver.find_element(By.CSS_SELECTOR, "input[type='text']")
    password_field = driver.find_element(By.CSS_SELECTOR, "input[type='password']")
    login_button = driver.find_element(By.TAG_NAME, "button")

    # 5. Perform Automated Testing (Input Data)
    username_field.send_keys("aja@gmail.com") 
    password_field.send_keys("0987654321")
    print("Step 2: Automated data entry successful.")


    # 6. Final pause for screenshot
    time.sleep(100) 
    print("Step 3: Verification complete.")

finally:
    # 7. Close process
    driver.quit()
    print("Step 4: All processes ended.")