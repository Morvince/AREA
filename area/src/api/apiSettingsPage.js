import axios from "axios"
import { useMutation } from "react-query"

const spotifyConnect = async (data) => {
  return await axios.post("/spotify/connect", data)
}

export const useSpotifyConnect = () => {
  return useMutation(spotifyConnect, {
    onSuccess: (data) => {
      window.location.replace(data.data.authorization_url)
    }
  })
}

const spotifyAccess = async (data) => {
  return await axios.post("/spotify/get_access_token", data)
}

export const useSpotifyAccess = () => {
  return useMutation(spotifyAccess)
}