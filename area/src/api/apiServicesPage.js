import axios from "axios"
import { useMutation } from "react-query"

const getAction = async () => {
  return await axios.post("/action/get_all")
}

export const useGetAction = () => {
  return useMutation(getAction)
}

const addAutomation = async (data) => {
  return await axios.post("/automation/add", JSON.stringify({token: sessionStorage.getItem("token")}))
}

export const useAddAutomation = () => {
  return useMutation(addAutomation)
}
