import api from "../../utils/apiClient.js";

export const searchPeople = async (keyword) => {
    const response = await api.get('/api/v1/people/search', {
        params: {name: keyword}
    });

    return response.data;
}
