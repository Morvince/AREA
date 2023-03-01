import axios from "axios"
import { useMutation } from "react-query"

const getThreadType = async () => {
  return await axios.post("/discord/get_thread_type")
}

export const useGetThreadType = () => {
  return useMutation(getThreadType)
}