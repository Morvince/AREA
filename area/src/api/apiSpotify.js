import axios from "axios"
import { useMutation } from "react-query"


// function to get the user playlist from a user
const getUserPlaylist = async () => {
  return await axios.post("/spotify/get_user_playlists", JSON.stringify({token: sessionStorage.getItem("token")}))
}

// function that call the getUserPlaylist
export const useGetUserPlaylist = () => {
  return useMutation(getUserPlaylist)
}