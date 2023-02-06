import axios from "axios"
import { useMutation } from "react-query"

const getAction = async () => {
  return await axios.post("/action/get_all", JSON.stringify({token: sessionStorage.getItem("actions")}))
}

export const useGetAction = () => {
  return useMutation(getAction)
}