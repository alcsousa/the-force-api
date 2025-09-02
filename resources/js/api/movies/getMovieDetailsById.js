import api from "../../utils/apiClient.js";

export const getMovieDetailsById = async (id) => {
    const response = await api.get(`/api/v1/films/${id}`);

    return response.data;
}
