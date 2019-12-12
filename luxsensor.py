# Write your code here :-)
import time
from datetime import datetime
import board
import busio
import adafruit_veml7700
import pymysql

db = pymysql.connect(host='remotemysql.com', user='nah fam', password='nah fam', db='sUDg9UwluY')
cur = db.cursor(pymysql.cursors.DictCursor)
'''
sql = "SELECT * FROM lightsensor"
cur.execute(sql)
for row in cur:
    print("Time: ", row['Date'])
    print("Light: ", row['Light'])
    print("Lux", row['Lux'])
    print("")

input("Press enter key to continue...")
'''
i2c = busio.I2C(board.SCL, board.SDA)
veml7700 = adafruit_veml7700.VEML7700(i2c)

while (True):
    #file = open("/home/pi/Documents/Python Programs/Entry/data.txt", "a")
    now = datetime.now()
    #date_time = now.strftime("%m/%d/%Y, %I:%M %p")
    '''
    file.write(date_time)
    file.write("\nAmbient light: ")
    file.write('{}'.format(veml7700.light))
    file.write("\nLux: ")
    file.write('{}'.format(veml7700.lux))
    file.write("\n\n")
    file.close()
    '''
    print("Ambient Light: ", veml7700.light)
    print("Lux: ", veml7700.lux)
    sql = "INSERT INTO `lightsensor` (`Date`, `Light`, `Lux`) VALUES (%s, %s, %s)"
    cur.execute(sql, (now, veml7700.light, veml7700.lux))
    db.commit()
    print("Successfully updated database!")
    print("")

    time.sleep(3)