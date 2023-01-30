import axios from "axios"
import { useMutation } from "react-query"
import { redirect } from "react-router-dom"

const login = async (formData) => {
  return await axios.post("/login", formData)
}

export const useLogin = () => {
  return useMutation(login, {
    onSuccess: (data) => {
      console.log(data)
      redirect("/")
      // sessionStorage.setItem("user_id", data.data)
    }
  })
}

const register = async (formData) => {
  return await axios.post("/register", formData)
}

export const useRegister = () => {
  return useMutation(register, {
    onSuccess: (data) => {
      console.log(data)
      redirect("/")
      // sessionStorage.setItem("user_id", data.data)
    }
  })
}