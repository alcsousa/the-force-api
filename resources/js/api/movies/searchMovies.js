import api from "../../utils/apiClient.js";

export const searchMovies = async (keyword) => {
    const response = await api.get('/api/v1/films/search', {
        params: {title: keyword}
    });

    return response.data;
}
