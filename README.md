# Etisalat-Payment-API
Etisalat Payment Gateway API

# Description: 
Etisalat Payment documentation : https://www.epg-sandbox.etisalat.ae/

In Etisalat payment process we need to follow 2 step to complete payment: 

<b> Step 1:</b> Registration of Transaction
First we need to send request to register transaction, in this request we need to pass following parameters
API customer,  Username, Password,  Amount, Order ID, Return URL
In the Response we get Transaction ID which is generated from Etisalat gateway. 

<b> Step 2: </b> Process Transaction
After getting Transaction ID we can send an other request to perform the transaction
We need to send Transaction ID (which we got in first step)



