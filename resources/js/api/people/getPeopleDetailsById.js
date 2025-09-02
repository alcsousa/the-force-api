import api from "../../utils/apiClient.js";

export const getPeopleDetailsById = async (id) => {
    const response = await api.get(`/api/v1/people/${id}`);

    return response.data;
}
