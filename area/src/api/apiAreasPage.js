import axios from "axios"
import { useMutation } from "react-query"

const getInfosAreas = async () => {
    return await axios.post("/automation/get", JSON.stringify({token: sessionStorage.getItem("token")}))
};

export const useGetInfosAreas = () => {
    return useMutation(getInfosAreas)
};