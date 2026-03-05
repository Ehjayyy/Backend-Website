
const axios = require('axios');

async function testRegisterSeller() {
    try {
        const response = await axios.post('https://marketplace-backend-t6d6.onrender.com/api/auth/register', {
            name: 'Test Seller',
            email: 'testseller@example.com',
            password: 'testpassword123',
            role: 'SELLER'
        });
        console.log('✅ Seller register successful:', response.data);
    } catch (error) {
        console.error('❌ Seller register failed:', error.response?.data || error.message);
    }
}

testRegisterSeller();
