import axios from "axios"
import { useMutation } from "react-query"

const getUserRepos = async () => {
  return await axios.post("/github/get_user_repos", JSON.stringify({token: sessionStorage.getItem("token")}))
}

export const useGetUserRepos = () => {
  return useMutation(getUserRepos)
}