
const axios = require('axios');

async function testLogin() {
    try {
        const response = await axios.post('https://marketplace-backend-t6d6.onrender.com/api/auth/login', {
            email: 'testbuyer@example.com',
            password: 'testpassword123'
        });
        console.log('✅ Login successful:', response.data);
    } catch (error) {
        console.error('❌ Login failed:', error.response?.data || error.message);
    }
}

testLogin();
