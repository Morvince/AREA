import axios from "axios"
import { useMutation } from "react-query"

const getAllActions = async () => {
  return await axios.post("/action/get_all")
}

export const useGetAllActions = () => {
  return useMutation(getAllActions)
}