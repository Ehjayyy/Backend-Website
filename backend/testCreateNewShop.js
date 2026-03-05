import axios from 'axios';

async function testCreateNewShop() {
  const baseUrl = 'https://marketplace-backend-t6d6.onrender.com';
  const newUser = {
    name: 'New Test Seller',
    email: 'newtestseller@example.com',
    password: 'testpassword123',
    role: 'SELLER'
  };

  try {
    // Register new user
    console.log('Registering new seller...');
    const registerResponse = await axios.post(`${baseUrl}/api/auth/register`, newUser);
    console.log('✅ Registration successful');
    const token = registerResponse.data.token;

    // Create shop
    const shopData = {
      shop_name: 'New Test Shop',
      description: 'This is a new test shop'
    };

    console.log('Creating shop...');
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

testCreateNewShop();
