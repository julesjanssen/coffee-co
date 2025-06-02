import { hideAllPoppers } from 'floating-vue'
import { ref } from 'vue'

import type { SystemTaskStatus } from '../shared/constants'
import { http } from '../shared/http'

interface SystemTaskOptions {
  pollInterval?: number
  maxTries?: number
}

interface SystemTask {
  id: string
  status: keyof typeof SystemTaskStatus
  startedAt?: string
  completedAt?: string
  links: Record<string, string>
}

export function useTask() {
  const isRunning = ref(false)

  const startTask = async (
    callback: () => Promise<SystemTask> | SystemTask,
    options: SystemTaskOptions = {},
  ): Promise<SystemTask> => {
    const { pollInterval = 3_000, maxTries = 30 } = options
    const { promise, resolve, reject } = Promise.withResolvers<SystemTask>()

    isRunning.value = true

    try {
      // Execute the callback to get the initial task
      const initialTask = await callback()
      let tries = 0

      const pollTask = async (): Promise<void> => {
        tries++

        try {
          const response = await http.get(initialTask.links.view)
          const task: SystemTask = response.data

          if (task.status === 'completed') {
            isRunning.value = false
            resolve(task)
          } else if (task.status === 'failed') {
            isRunning.value = false
            reject(new Error('Task failed'))
          } else {
            if (tries >= maxTries) {
              isRunning.value = false
              reject(new Error('Task failed'))
              return
            }

            // Task still running, poll again
            setTimeout(pollTask, pollInterval)
          }
        } catch (error) {
          isRunning.value = false
          reject(error)
        }
      }

      // Start polling
      pollTask()
    } catch (error) {
      isRunning.value = false
      reject(error)
    }

    return promise
  }

  const downloadTaskResult = (task: SystemTask) => {
    const link = document.createElement('a')
    link.href = task.links.download
    link.download = ''
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
  }

  const executeAndDownloadTask = (
    endpoint: string,
    payload?: Record<string, any>,
    options: SystemTaskOptions = {},
  ): Promise<SystemTask> => {
    return startTask(async () => {
      const response = await http.post(endpoint, payload)
      return response.data
    }, options).then((task) => {
      if (task.links?.download) {
        hideAllPoppers()
        downloadTaskResult(task)
      }

      return task
    })
  }

  return {
    isRunning,
    downloadTaskResult,
    startTask,
    executeAndDownloadTask,
  }
}
