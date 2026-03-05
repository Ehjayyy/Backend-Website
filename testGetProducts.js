
const axios = require('axios');

async function testGetProducts() {
    try {
        const response = await axios.get('https://marketplace-backend-t6d6.onrender.com/api/products');
        console.log('✅ Products list:', response.data);
    } catch (error) {
        console.error('❌ Failed to get products:', error.response?.data || error.message);
    }
}

testGetProducts();
