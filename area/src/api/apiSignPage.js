import axios from "axios"
import { useMutation } from "react-query"

//function that initialise the login
const login = async (formData) => {
  return await axios.post("/login", formData)
}

// Functioni that allow you to use the login
export const useLogin = () => {
  return useMutation(login, {
    onSuccess: (data) => {
      sessionStorage.setItem("token", data.data.token)
    }
  })
}

// function that allow someone to register
const register = async (formData) => {
  return await axios.post("/register", formData)
}

// function that call the register function
export const useRegister = () => {
  return useMutation(register, {
    onSuccess: (data) => {
      sessionStorage.setItem("token", data.data.token)
    }
  })
}