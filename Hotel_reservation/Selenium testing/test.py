from selenium import webdriver
import time

# Start Chrome
driver = webdriver.Chrome()

# Open Google as a test
driver.get("https://www.google.com")
print("Automation Successful: Selenium is controlling Chrome!")


time.sleep(5)

# Close the process
driver.quit()