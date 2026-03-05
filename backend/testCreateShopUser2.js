import axios from 'axios';

async function testCreateShopForUser2() {
  const baseUrl = 'https://marketplace-backend-t6d6.onrender.com';
  const loginData = {
    email: 'early@gmail.com',
    password: 'password123'
  };

  try {
    // Login to get token
    const loginResponse = await axios.post(`${baseUrl}/api/auth/login`, loginData);
    console.log('✅ Login successful');
    const token = loginResponse.data.token;
    console.log('Token:', token);

    // Create shop
    const shopData = {
      shop_name: 'Test Shop 2',
      description: 'This is a test shop created by user 2'
    };

    const shopResponse = await axios.post(`${baseUrl}/api/shops`, shopData, {
      headers: {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    });

    console.log('✅ Shop created successfully');
    console.log('Shop:', shopResponse.data);
  } catch (error) {
    console.error('❌ Error:', error.response?.data || error.message);
  }
}

testCreateShopForUser2();
