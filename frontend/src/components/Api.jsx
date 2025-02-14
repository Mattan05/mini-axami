const API_BASE_URL = 'http://localhost/mini-axami/public/api'; // Symfony-backendens bas-URL

export const fetchData = async () => {
    try {
        const response = await fetch(`${API_BASE_URL}/data`, {
            method: 'GET', // HTTP-metod
            headers: {
                'Content-Type': 'application/json',
            },
        });

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching data:', error);
        throw error;
    }
};
