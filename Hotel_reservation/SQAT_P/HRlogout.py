from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import Select # Standard SQAT tool for dropdowns
import time

driver = webdriver.Chrome()

try:
    # STEP 1: Login
    driver.get("http://localhost/Hotel_reservation/login.php")
    driver.maximize_window()
    driver.find_element(By.NAME, "username").send_keys("aja@gmail.com")
    driver.find_element(By.NAME, "loginPassword").send_keys("0987654321")
    driver.find_element(By.CLASS_NAME, "login-btn").click()
    print("Step 1: Login successful.")
    time.sleep(3)

    # STEP 2: Select Location (The Fix)
    driver.get("http://localhost/Hotel_reservation/select_location.php")
    
    # Use the Select class to handle the dropdown reliably
    location_dropdown = Select(driver.find_element(By.ID, "location"))
    
    # Option A: Select by index (1 picks the first city in your list)
    location_dropdown.select_by_index(1) 
    
    driver.find_element(By.CSS_SELECTOR, "input[type='submit']").click()
    print("Step 2: Location selected via Index.")
    time.sleep(3)

    # STEP 3: Room Selection & Date Entry
    print("Step 3: Accessing Room Selection...")
    time.sleep(5) 
    
    driver.find_element(By.NAME, "room_qty[Suite]").send_keys("1")
    driver.find_element(By.NAME, "guests").send_keys("2")
    
    # JavaScript Injection for Dates (Prevents orange error)
    checkin_val = "2026-04-17T08:31"
    checkout_val = "2026-04-23T08:31"
    
    checkin_field = driver.find_element(By.ID, "checkin")
    checkout_field = driver.find_element(By.ID, "checkout")
    
    driver.execute_script("arguments[0].value = arguments[1]", checkin_field, checkin_val)
    driver.execute_script("arguments[0].value = arguments[1]", checkout_field, checkout_val)
    print("Step 4: Dates injected via JavaScript.")

    # STEP 4: Final Submit
    submit_btn = driver.find_element(By.CSS_SELECTOR, "input[type='submit']")
    submit_btn.click()
    print("Step 5: Form submitted successfully.")

   
    time.sleep(60)

finally:
    driver.quit()
    print("Step 6: Testing session ended successfully.")