import axios from "axios"
import { useMutation } from "react-query"

const getThreadType = async () => {
  return await axios.post("/discord/get_user_channels", JSON.stringify({token: sessionStorage.getItem("token")}))
}

export const useGetThreadType = () => {
  return useMutation(getThreadType)
}