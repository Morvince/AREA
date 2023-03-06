import axios from "axios"
import { useMutation } from "react-query"

const sendConfirmation = async (data) => {
  return await axios.post("/send_confirmation", JSON.stringify({ token: sessionStorage.getItem("token")}))
}

export const useSendConfirmation = () => {
  return useMutation(sendConfirmation)
}

const validate = async (data) => {
  return await axios.post("/validate", JSON.stringify({ token: sessionStorage.getItem("token")}))
}

export const useValidate = () => {
  return useMutation(validate)
}

const validated = async (data) => {
  return await axios.post("/validated", JSON.stringify({ token: sessionStorage.getItem("token")}))
}

export const useValidated = () => {
  return useMutation(validated)
}
