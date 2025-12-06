import axios from 'axios';

// Ganti dengan IP komputer Anda
const API_BASE_URL = 'http://ISI IP/storage_api';

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
  timeout: 10000, // 10 detik timeout
});

// Add interceptor untuk logging
api.interceptors.request.use(
  (config) => {
    console.log('API Request:', config.method.toUpperCase(), config.url);
    console.log('Data:', config.data);
    return config;
  },
  (error) => {
    console.error('Request Error:', error);
    return Promise.reject(error);
  }
);

api.interceptors.response.use(
  (response) => {
    console.log('API Response:', response.data);
    return response;
  },
  (error) => {
    console.error('Response Error:', error.response?.data || error.message);
    return Promise.reject(error);
  }
);

// Auth API
export const registerUser = async (userData) => {
  try {
    const response = await api.post('/register.php', userData);
    return response.data;
  } catch (error) {
    console.error('Register Error:', error);
    throw error;
  }
};

export const loginUser = async (credentials) => {
  try {
    const response = await api.post('/login.php', credentials);
    return response.data;
  } catch (error) {
    console.error('Login Error:', error);
    throw error;
  }
};

// Items CRUD API
export const getItems = async () => {
  try {
    const response = await api.get('/get_items.php');
    return response.data;
  } catch (error) {
    console.error('Get Items Error:', error);
    throw error;
  }
};

export const createItem = async (itemData) => {
  try {
    console.log('Creating item:', itemData);
    const response = await api.post('/create_item.php', itemData);
    console.log('Create response:', response.data);
    return response.data;
  } catch (error) {
    console.error('Create Item Error:', error);
    if (error.response) {
      console.error('Error response:', error.response.data);
    }
    throw error;
  }
};

export const updateItem = async (id, itemData) => {
  try {
    const response = await api.put(`/update_item.php?id=${id}`, itemData);
    return response.data;
  } catch (error) {
    console.error('Update Item Error:', error);
    throw error;
  }
};

export const deleteItem = async (id) => {
  try {
    const response = await api.delete(`/delete_item.php?id=${id}`);
    return response.data;
  } catch (error) {
    console.error('Delete Item Error:', error);
    throw error;
  }
};

export default api;
