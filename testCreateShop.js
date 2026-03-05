
const axios = require('axios');

async function testCreateShop() {
    try {
        // First, login as the seller
        const loginResponse = await axios.post('https://marketplace-backend-t6d6.onrender.com/api/auth/login', {
            email: 'testseller@example.com',
            password: 'testpassword123'
        });
        
        const token = loginResponse.data.token;
        
        // Now create a shop
        const shopResponse = await axios.post('https://marketplace-backend-t6d6.onrender.com/api/shops', {
            shop_name: 'Test Shop',
            description: 'This is a test shop'
        }, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
        
        console.log('✅ Shop created successfully:', shopResponse.data);
    } catch (error) {
        console.error('❌ Shop creation failed:', error.response?.data || error.message);
    }
}

testCreateShop();
