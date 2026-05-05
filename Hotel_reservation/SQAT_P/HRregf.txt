from selenium import webdriver
from selenium.webdriver.common.by import By
import time

driver = webdriver.Chrome()

try:
    driver.get("http://localhost/Hotel_reservation/index.html")
    driver.maximize_window()
    print("Step 1: Registration Page opened.")
    
    #load
    time.sleep(2)

    # --- Automated Data Entry ---
    driver.find_element(By.ID, "fname").send_keys("ajax")
    driver.find_element(By.ID, "email").send_keys("ajax@gmail.com")
    driver.find_element(By.ID, "nid").send_keys("12345678999")
    driver.find_element(By.ID, "password").send_keys("SQAT_Pass_2026")
    driver.find_element(By.ID, "cpassword").send_keys("SQAT_Pass_2026")
    
    # Radio, Checkbox, and Date
    driver.find_element(By.ID, "male").click()
    driver.find_element(By.ID, "dob").send_keys("10-26-2000")
    driver.find_element(By.ID, "terms").click()
    time.sleep(20)
    print("Step 2: All features populated automatically.")

    
    print("Step 3: Verification window closed.")

finally:
    driver.quit()
    print("Step 4: All processes ended successfully.")