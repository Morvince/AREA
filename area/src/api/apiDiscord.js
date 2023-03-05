import axios from "axios"
import { useMutation } from "react-query"

const getUserChannels = async () => {
  return await axios.post("/discord/get_user_channels", JSON.stringify({token: sessionStorage.getItem("token")}))
}

export const useGetUserChannels = () => {
  return useMutation(getUserChannels)
}