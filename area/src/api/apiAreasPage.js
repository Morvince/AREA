import axios from "axios"
import { useMutation } from "react-query"

// function to get infos from the db about the areas
const getInfosAreas = async () => {
    return await axios.post("/automation/get", JSON.stringify({token: sessionStorage.getItem("token")}))
};

// function which call the getinfos areas
export const useGetInfosAreas = () => {
    return useMutation(getInfosAreas)
};

// function that makes the request to delete an area
const deleteInfosAreas = async (data) => {
    return await axios.post("/automation/delete", JSON.stringify({automation_id: data.id}))
};


// function which call the deleteinfosAreas 
export const useDeleteInfosAreas = () => {
    return useMutation(deleteInfosAreas)
};