import axios from "axios"
import { useMutation } from "react-query"

const login = async (formData) => {
  return await axios.post("/login", formData)
}

export const useLogin = () => {
  return useMutation(login, {
    onSuccess: () => {
      console.log("test")
    }
  })
}

const register = async (formData) => {
  return await axios.post("/register", formData)
}

export const useRegister = () => {
  return useMutation(register, {
    onSuccess: () => {
      console.log("test2")
    }
  })
}