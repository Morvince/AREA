import axios from "axios"
import { useMutation } from "react-query"

const getInfosAreas = async () => {
    return await axios.post("/automation/get", JSON.stringify({token: sessionStorage.getItem("token")}))
};

export const useGetInfosAreas = () => {
    return useMutation(getInfosAreas)
};

const deleteInfosAreas = async (data) => {
    return await axios.post("/automation/delete", JSON.stringify({automation_id: data.id}))
};

export const useDeleteInfosAreas = () => {
    return useMutation(deleteInfosAreas)
};