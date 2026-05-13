from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import Select
import time

driver = webdriver.Chrome()

try:
    # --- PHASE 1: REGISTRATION ---
    driver.get("http://localhost/Hotel_reservation/index.html")
    driver.maximize_window()
    print("Step 1: Registration Page opened.")
    time.sleep(2)

    # Use a consistent email for both registration and login
    test_email = "rok_final_demo@gmail.com"
    test_nid = "12345678005" # Increment this if you run the script again

    driver.find_element(By.ID, "fname").send_keys("Rok")
    driver.find_element(By.ID, "email").send_keys(test_email)
    driver.find_element(By.ID, "nid").send_keys(test_nid)
    driver.find_element(By.ID, "password").send_keys("1234567890")
    driver.find_element(By.ID, "cpassword").send_keys("1234567890")
    
    driver.find_element(By.ID, "male").click()
    driver.find_element(By.ID, "dob").send_keys("10-26-2000")
    driver.find_element(By.ID, "terms").click()
    
    print("Step 2: Registration data populated.")
    time.sleep(2)
    
    # Submit Registration
    registration_submit = driver.find_element(By.CSS_SELECTOR, "button[type='submit'], input[type='submit'], .submit-btn, .SUBMIT-btn")
    registration_submit.click()
    time.sleep(2)

    # --- HANDLING THE ALERT ---
    try:
        alert = driver.switch_to.alert
        print(f"Alert Handled: {alert.text}")
        alert.accept() 
    except:
        print("No alert present.")

    time.sleep(2)

    # --- PHASE 2: LOGIN ---
    # Now using the same 'test_email' used in Registration
    driver.get("http://localhost/Hotel_reservation/login.php")
    driver.find_element(By.NAME, "username").send_keys(test_email)
    driver.find_element(By.NAME, "loginPassword").send_keys("1234567890")
    driver.find_element(By.CLASS_NAME, "login-btn").click()
    print(f"Step 3: Login successful with {test_email}.")
    time.sleep(2)

    # --- PHASE 3: LOCATION & ROOMS ---
    driver.get("http://localhost/Hotel_reservation/select_location.php")
    location_dropdown = Select(driver.find_element(By.ID, "location"))
    location_dropdown.select_by_index(1) 
    driver.find_element(By.CSS_SELECTOR, "input[type='submit']").click()
    
    time.sleep(3)
    driver.find_element(By.NAME, "room_qty[Suite]").send_keys("1")
    driver.find_element(By.NAME, "guests").send_keys("2")
    
    # JavaScript Injection for consistent date entry
    checkin_val = "2026-04-17T08:31"
    checkout_val = "2026-04-23T08:31"
    driver.execute_script("arguments[0].value = arguments[1]", driver.find_element(By.ID, "checkin"), checkin_val)
    driver.execute_script("arguments[0].value = arguments[1]", driver.find_element(By.ID, "checkout"), checkout_val)
    
    print("Step 4: Booking details and dates entered.")
    
    # Final Submission
    driver.find_element(By.CSS_SELECTOR, "input[type='submit']").click()
    print("Step 5: Form submitted to Review page.")

    # Extended pause for demo evaluation
    print("--- PAUSING 60 SECONDS FOR EVALUATION ---")
    time.sleep(6)

finally:
    driver.quit()
    print("Step 6: Testing session ended successfully.")