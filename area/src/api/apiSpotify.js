import axios from "axios"
import { useMutation } from "react-query"

const getUserPlaylist = async () => {
  return await axios.post("/spotify/get_user_playlists", JSON.stringify({token: sessionStorage.getItem("token")}))
}

export const useGetUserPlaylist = () => {
  return useMutation(getUserPlaylist)
}