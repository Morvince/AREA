import axios from "axios"
import { useMutation } from "react-query"

const login = async (formData) => {
  return await axios.post("/login", formData)
}

export const useLogin = () => {
  return useMutation(login, {
    onSuccess: (data) => {
      sessionStorage.setItem("token", data.data.token)
    }
  })
}

const register = async (formData) => {
  return await axios.post("/register", formData)
}

export const useRegister = () => {
  return useMutation(register, {
    onSuccess: (data) => {
      sessionStorage.setItem("token", data.data.token)
    }
  })
}