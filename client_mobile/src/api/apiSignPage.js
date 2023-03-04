import axios from "axios"
import { useMutation } from "react-query"
import AsyncStorage from '@react-native-async-storage/async-storage';

const login = async (formData) => {
  return await axios.post("/login", formData)
}

export const useLogin = () => {
  return useMutation(login, {
    onSuccess: (data) => {
      AsyncStorage.setItem("token", data.data.token)
      .catch((error) => console.log(error))
    }
  })
}

const register = async (formData) => {
  return await axios.post("/register", formData)
}

export const useRegister = () => {
  return useMutation(register, {
    onSuccess: (data) => {
      AsyncStorage.setItem("token", data.data.token)
      .catch((error) => console.log(error))
    }
  })
}